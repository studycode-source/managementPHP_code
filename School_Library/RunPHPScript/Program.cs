using System;
using System.Diagnostics;

namespace RunPHPScript
{
    class Program
    {
        static void Main(string[] args)
        {
            try
            {
                // Path to PHP executable
                string phpPath = @"C:\xampp\php\php.exe";

                // Path to PHP script (passed as an argument or hardcoded)
                string scriptPath = args.Length > 0 ? args[0] : @"setup_database.php";

                // Start the PHP process
                ProcessStartInfo startInfo = new ProcessStartInfo();
                startInfo.FileName = phpPath;
                startInfo.Arguments = scriptPath;
                startInfo.UseShellExecute = false;
                startInfo.RedirectStandardOutput = true;
                startInfo.RedirectStandardError = true;

                using (Process process = Process.Start(startInfo))
                {
                    // Read the output (if any)
                    string output = process.StandardOutput.ReadToEnd();
                    string error = process.StandardError.ReadToEnd();

                    // Wait for the process to exit
                    process.WaitForExit();

                    // Log the output and errors
                    Console.WriteLine("Output: " + output);
                    if (!string.IsNullOrEmpty(error))
                    {
                        Console.WriteLine("Error: " + error);
                    }
                }
            }
            catch (Exception ex)
            {
                Console.WriteLine("Exception: " + ex.Message);
            }
        }
    }
}